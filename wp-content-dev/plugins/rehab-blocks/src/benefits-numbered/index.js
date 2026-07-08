import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const items = Array.isArray( a.items ) ? a.items : [];
		const update = ( i, k, v ) => {
			const next = items.slice();
			next[ i ] = { ...next[ i ], [ k ]: v };
			setAttributes( { items: next } );
		};
		const add = () => setAttributes( { items: [ ...items, { title: '', body: '' } ] } );
		const remove = ( i ) => setAttributes( { items: items.filter( ( _, j ) => j !== i ) } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Benefits" initialOpen>
						{ items.map( ( _item, i ) => (
							<div key={ i } style={ { display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' } }>
								<span>Benefit { i + 1 }</span>
								<Button variant="link" isDestructive onClick={ () => remove( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ add }>Add benefit</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="rehab-benefits-numbered">
						{ items.map( ( item, i ) => (
							<div className="rehab-benefit" key={ i }>
								<div className="rehab-benefit__num">{ String( i + 1 ).padStart( 2, '0' ) }</div>
								<div className="rehab-benefit__body">
									<RichText tagName="h3" value={ item.title } onChange={ ( v ) => update( i, 'title', v ) } placeholder="Benefit title" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									<RichText tagName="p" value={ item.body } onChange={ ( v ) => update( i, 'body', v ) } placeholder="Benefit body…" allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
								</div>
							</div>
						) ) }
					</div>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		const items = Array.isArray( a.items ) ? a.items : [];
		return (
			<div className="rehab-benefits-numbered">
				{ items.map( ( item, i ) => (
					<div className="rehab-benefit" key={ i }>
						<div className="rehab-benefit__num">{ String( i + 1 ).padStart( 2, '0' ) }</div>
						<div className="rehab-benefit__body">
							<RichText.Content tagName="h3" value={ item.title } />
							<RichText.Content tagName="p" value={ item.body } />
						</div>
					</div>
				) ) }
			</div>
		);
	},
} );

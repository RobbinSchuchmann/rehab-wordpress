import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function Markup( { a } ) {
	const items = Array.isArray( a.items ) ? a.items : [];
	return (
		<div className="rehab-benefits-numbered">
			{ items.map( ( item, i ) => (
				<div className="rehab-benefit" key={ i }>
					<div className="rehab-benefit__num">{ String( i + 1 ).padStart( 2, '0' ) }</div>
					<div className="rehab-benefit__body">
						<h4>{ item.title }</h4>
						<p>{ item.body }</p>
					</div>
				</div>
			) ) }
		</div>
	);
}

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
						{ items.map( ( item, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', paddingBottom: '1rem', marginBottom: '1rem' } }>
								<TextControl label={ `Benefit ${ i + 1 } title` } value={ item.title } onChange={ ( v ) => update( i, 'title', v ) } />
								<TextareaControl label="Body" value={ item.body } onChange={ ( v ) => update( i, 'body', v ) } />
								<Button variant="link" isDestructive onClick={ () => remove( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ add }>Add benefit</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Markup a={ a } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		return <Markup a={ attributes } />;
	},
} );
